package com.example.mindcare.ui.screens

import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.PaddingValues
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material3.Button
import androidx.compose.material3.ExperimentalMaterial3Api
import androidx.compose.material3.FloatingActionButton
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.ModalBottomSheet
import androidx.compose.material3.OutlinedTextField
import androidx.compose.material3.Scaffold
import androidx.compose.material3.Text
import androidx.compose.material3.TextButton
import androidx.compose.material3.icons.Icons
import androidx.compose.material3.icons.filled.Add
import androidx.compose.material3.icons.filled.Mic
import androidx.compose.material3.icons.filled.PhotoCamera
import androidx.compose.runtime.Composable
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.setValue
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import com.example.mindcare.data.FakeData
import com.example.mindcare.model.JournalEntry
import com.example.mindcare.model.Mood
import com.example.mindcare.ui.components.GradientHeader
import com.example.mindcare.ui.components.JournalEntryCard
import com.example.mindcare.ui.components.MoodSelector

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun JournalScreen(
    innerPadding: PaddingValues,
    onAddTextEntry: () -> Unit,
    onAddVoiceEntry: () -> Unit,
    onAddPhotoEntry: () -> Unit
) {
    var showNewEntrySheet by remember { mutableStateOf(false) }

    if (showNewEntrySheet) {
        NewJournalEntrySheet(
            onDismiss = { showNewEntrySheet = false },
            onSave = { entry ->
                FakeData.journalEntries.add(0, entry)
                showNewEntrySheet = false
            }
        )
    }

    Scaffold(
        modifier = Modifier
            .fillMaxSize()
            .padding(innerPadding),
        floatingActionButton = {
            FloatingActionButton(onClick = { showNewEntrySheet = true }) {
                Icon(Icons.Default.Add, contentDescription = "Add entry")
            }
        }
    ) { contentPadding ->
        LazyColumn(
            modifier = Modifier
                .fillMaxSize()
                .padding(contentPadding)
        ) {
            item {
                GradientHeader(
                    title = "Your journal space",
                    subtitle = "Capture thoughts, voice notes, and photos.",
                    emoji = "📓"
                )
            }
            item {
                Spacer(modifier = Modifier.height(16.dp))
                Column(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(horizontal = 20.dp),
                    verticalArrangement = Arrangement.spacedBy(8.dp)
                ) {
                    Text(text = "Add entry")
                    androidx.compose.foundation.layout.Row(
                        horizontalArrangement = Arrangement.spacedBy(12.dp)
                    ) {
                        IconButton(onClick = { showNewEntrySheet = true }) {
                            Icon(Icons.Default.Add, contentDescription = "Text")
                        }
                        IconButton(onClick = onAddVoiceEntry) {
                            Icon(Icons.Default.Mic, contentDescription = "Voice")
                        }
                        IconButton(onClick = onAddPhotoEntry) {
                            Icon(Icons.Default.PhotoCamera, contentDescription = "Photo")
                        }
                    }
                }
            }
            item {
                Spacer(modifier = Modifier.height(16.dp))
                Text(
                    text = "Recent entries",
                    modifier = Modifier.padding(horizontal = 20.dp)
                )
            }
            items(FakeData.journalEntries) { entry ->
                JournalEntryCard(entry = entry)
            }
            item { Spacer(modifier = Modifier.height(24.dp)) }
        }
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
private fun NewJournalEntrySheet(
    onDismiss: () -> Unit,
    onSave: (JournalEntry) -> Unit
) {
    var title by remember { mutableStateOf("") }
    var content by remember { mutableStateOf("") }
    var mood by remember { mutableStateOf<Mood?>(Mood.OKAY) }

    ModalBottomSheet(onDismissRequest = onDismiss) {
        Column(
            modifier = Modifier
                .padding(horizontal = 20.dp, vertical = 16.dp)
        ) {
            Text(text = "New text entry")
            Spacer(modifier = Modifier.height(12.dp))
            OutlinedTextField(
                value = title,
                onValueChange = { title = it },
                label = { Text("Title") },
                modifier = Modifier.fillMaxWidth()
            )
            Spacer(modifier = Modifier.height(8.dp))
            OutlinedTextField(
                value = content,
                onValueChange = { content = it },
                label = { Text("What's on your mind?") },
                modifier = Modifier
                    .fillMaxWidth()
                    .height(120.dp)
            )
            Spacer(modifier = Modifier.height(12.dp))
            Text(text = "Mood tag")
            MoodSelector(selected = mood, onMoodSelected = { mood = it })

            Spacer(modifier = Modifier.height(20.dp))
            Button(
                onClick = {
                    val entry = JournalEntry(
                        id = "local_${System.currentTimeMillis()}",
                        title = title.ifBlank { "Untitled entry" },
                        content = content,
                        timestamp = System.currentTimeMillis(),
                        mood = mood ?: Mood.OKAY
                    )
                    onSave(entry)
                },
                modifier = Modifier.fillMaxWidth(),
                enabled = title.isNotBlank() || content.isNotBlank()
            ) {
                Text("Save entry")
            }
            TextButton(
                onClick = onDismiss,
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(top = 8.dp)
            ) {
                Text("Cancel")
            }
            Spacer(modifier = Modifier.height(12.dp))
        }
    }
}


